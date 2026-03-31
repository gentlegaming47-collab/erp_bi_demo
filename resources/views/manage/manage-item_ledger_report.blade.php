@extends('layouts.app',['pageTitle' => 'Item Ledger Report'])

@section('header')
<style>
    .dataTables_filter {
    top: 35px;
    }
</style>
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>Item Ledger Report</li>
</ul>
@endsection

@section('content')
<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">
           @if(hasAccess("item_ledger_report","print"))
                {{-- <a href="{{route('print-item_ledger_report')}}" class="btn btn-inverse pre2" target="_blank" id="">Print PDF</a> --}}
           @endif
           <a href="javascript:void(0)" class="btn btn-inverse pre2" id="export-excel">Export</a>
        </div>
        <h4 class="widgettitle">Item Ledger Report</h4>
    </div>
    <div class="widgetcontent overflow-scroll">
        {{-- <h4>
        <span class="redbox"></span> 
            Indicates low stock Qty.
        <span class="yellowbox"></span> 
             Indicates more than maximum stock Qty. 
        </h4> --}}
        <table id="dyntable" class="table table-infinite table-bordered responsive table-autowidth">
            <thead>
                <tr class="main-header">
                    <th class="head0">Item Name</th>
                    <th class="head1">Item Code</th>
                    <th class="head0">Stock In</th>
                    <th class="head1">Stock Out</th>
                    <th class="head0">Stock Qty.</th>

                </tr>
            </thead>
        </table>
        <div class="note-text">Note: To search across multiple columns, add a space between words.</div>
    </div>
</div>
@endsection

@section('scripts')
<script>
jQuery(document).ready(function() {
var headerOpt = {'Authorization':'Bearer {{ Auth::user()->auth_token }}','X-CSRF-TOKEN':'{{ csrf_token() }}'};
jQuery('#export-excel').on('click',function(){
    jQuery('.export_item_ledger_report').click();
});
var table= jQuery('#dyntable').DataTable({
    "processing": true,
    "serverSide": true,
    "scrollX":true,
    "sScrollX": "100%",
    "sScrollXInner": "110%",
    "bScrollCollapse": true,
    "order": [[ 1, 'asc' ]],
    pageLength : 25,
    dom: 'Blfrtip',
    buttons:
            [
                {
                    extend:'excel',
                    filename: 'Item Ledger Report',
					title:"",
                    className: 'export_item_ledger_report d-none',
                     exportOptions: {
                       // columns: ':not(:eq(0))',
                        modifier: {
                            page: 'all'
                        }
                    },
                    action: newexportaction

                }
            ],

            ajax: {
                    url: "{{ route('listing-item_ledger_report') }}",
                    type: "POST",
                    headers: headerOpt,
                    error: function (jqXHR, textStatus, errorThrown){
                        jQuery('#dyntable_processing').hide();
                        if(jqXHR.status == 401){
                            toastError(jqXHR.statusText);
                        }else{
                        toastError('Somthing went wrong!');
                        }
                        console.log(JSON.parse(jqXHR.responseText));
                    }
            },

            columns: [
                { data: 'item_name' ,name: 'items.item_name' , },
                { data: 'item_code' ,name: 'items.item_code' , },
                { data: 'stock_in' ,name: 'stock_in' , },
                { data: 'stock_out' ,name: 'stock_out' , },
                { data: 'stock_qty' ,name: 'location_stock.stock_qty' ,},
            ],
             initComplete: function () {
                // Exclude first column (index 0) from search
                initColumnSearch('#dyntable', []);
            }

    });
});




</script>
@endsection
