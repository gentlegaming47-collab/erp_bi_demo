@extends('layouts.app',['pageTitle' => 'GRN Against PO Approval'])

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
    </style>

<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>Excess GRN Qty. Approval against PO</li>
</ul>
@endsection


@section('content')
<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">
            
          
        </div>

           
        <h4 class="widgettitle">Excess GRN Qty. Approval against PO</h4>
        
    </div>
    <div class="widgetcontent">
        
        <form id="commonGrnPOApprovalRequestForm" class="stdform" method="post">
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
                    
                                <h4 class="widgettitle"> GRN List</h4>
                                {{-- <h4 class="widgettitle">Raw Material Mapping</h4> --}}
                    
                            </div>
                 
                        <div class="widgetcontent overflow-scroll">
                            

                            
                            <table id="approvalTable" class="table table-infinite table-bordered responsive table-autowidth">
                            <thead>
                                <tr class="main-header">
                                   <th><input type="checkbox" name="checkall-sm" class="simple-check" id="checkall-sm"/></th>
                                    <th class="head0">GRN No.</th>
                                    <th class="head0">GRN Date</th>
                                    <th class="head1">Supplier</th>
                                    <th class="head1">Challan/Bill No.</th>
                                    <th class="head1">Date</th>
                                    <th class="head0">PO No.</th>
                                    <th class="head0">PO Date</th>
                                    <th class="head1">Item</th>
                                    <th class="head1">Code</th>
                                    <th class="head1">Group</th>
                                    <th class="head0">PO Qty.</th>                  
                                    <th class="head0">GRN Qty.</th>
                                    <th class="head0">Excess Qty.</th>
                                    <th class="head1">Unit </th>

                                </tr>
                            </thead>
                            <tbody>
                                <tr> <td colspan="17">No Data Available! </td> </tr>
                            </tbody>
                           
                            </table>
                            <div class="note-text">Note: To search across multiple columns, add a space between words.</div>
                          
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
    <script type="text/javascript" src="{{asset('js/view/grn_against_po_approval.js?ver='.getJsVersion()) }}"></script>
@endsection
