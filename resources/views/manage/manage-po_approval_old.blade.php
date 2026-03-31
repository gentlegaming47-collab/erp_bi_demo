@extends('layouts.app',['pageTitle' => 'PO Approval'])

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
    <li>PO Approval</li>
</ul>
@endsection


@section('content')
<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">
            
          
        </div>

           
        <h4 class="widgettitle">PO Approval</h4>
        
    </div>
    <div class="widgetcontent">
        
        <form id="commonPOApprovalRequestForm" class="stdform" method="post">
                <input type="hidden" name="pagename" id="pagename" value="po_approval">
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
                                <h4 class="widgettitle">PO List</h4>
                            </div>
                 
                            <div class="widgetcontent overflow-scroll">
                                <div style="display: flex;">
                                    <div  style="width: 48%;">  
                                      
                                    <table id="approvalTable" class="table table-infinite table-bordered responsive table-autowidth">
                                        <thead>
                                            {{-- <tr>
                                                <th><input type="checkbox" name="checkall-sm" class="simple-check" id="checkall-sm"/></th>
                                                <th class="head0">From Location</th>
                                                <th class="head0">PO No.</th>
                                                <th class="head0">PO Date</th>
                                                <th class="head0">To Location</th>
                                            </tr> --}}

                                            <tr>
                                                <th class="head0"></th>
                                                <th class="head1">From Location</th>
                                                <th class="head0">PO No.</th>
                                                <th class="head1">PO Date</th>
                                                <th class="head0">Supplier</th>
                                                <th class="head1">Person</th>
                                                <th class="head0">Ref. No. </th>
                                                <th class="head1">Ref. Date</th>
                                                <th class="head0">Ship To</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr> <td colspan="17">No Data Available! </td> </tr>
                                        </tbody>                                    
                                    </table>

                                    </div>
                                    
                                    <div style="width:2%;"></div>
                                    <div  style="width: 50%;">  
                                        <table id="approvalDataTable" class="table table-infinite table-bordered responsive table-autowidth">
                                            <thead>
                                                {{-- <tr>                                         
                                                    <th calss="head1">Item Name</th>
                                                    <th class="head0">Item Code</th>                    
                                                    <th class="head1">PO Qty.</th>                  
                                                    <th class="head0">Unit</th>                                                    
                                                </tr> --}}

                                                <tr>
                                                    <th class="head0">Sr. No.</th>
                                                    <th class="head1">Item</th>
                                                    <th class="head0">Code</th>
                                                    <th class="head1">Group</th>
                                                    <th class="head0">PO Qty.</th>
                                                    <th class="head1">Rate/Unit</th>
                                                    <th class="head0">Del. Date</th>
                                                    <th class="head1">Unit</th>
                                                    <th class="head0">Amount</th>
                                                    <th class="head1">Remark</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                               
                            </div>
                       
                    </div>
                            
                    <div class="row">
                        <div class="span-6">
                            <div class="par control-group form-control">
                            <label class="control-label" for="approved_by">Approved By </label>
                            <div class="controls"> <span class="formwrapper">
                                <input  type="text" name="approved_by" id="approved_by" class="input-large" value="{{Auth::user()->user_name}}" readonly />
                                </span> </div>
                            </div>
                        </div>
                    </div>
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
        </form>
    </div>
</div>
@endsection




@section('scripts')    
    <script type="text/javascript" src="{{asset('js/view/po_approval.js') }}"></script>
@endsection
