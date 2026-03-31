@extends('layouts.app',['pageTitle' => 'Report'])

@section('header')
<style>
    .dataTables_filter {
    top: 35px;
    }
</style>
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>Report</li>
</ul>
@endsection

@section('content')
<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">
           @if(hasAccess("report","print"))
                <a href="{{route('print-report')}}" class="btn btn-inverse pre2" target="_blank" id="">Print PDF</a>
           @endif
           <a href="javascript:void(0)" class="btn btn-inverse pre2" id="export-excel">Export</a>
        </div>
        <h4 class="widgettitle">Report</h4>
    </div>
    <div class="widgetcontent overflow-scroll">
        <h4>
        <form id="orderSearchForm" name="orderSearchForm" class="stdform">

        <span class="redbox"></span> 
            Indicates low stock Qty.
        <span class="yellowbox"></span> 
             Indicates more than maximum stock Qty.  
        <span>
            &nbsp; &nbsp; &nbsp;
            <select name="min_max" id="min_max" class="chzn-select"> 
                <option value="all">All</option>
                <option value="min_stock">Min. Stock</option>
                <option value="max_stock">Max. Stock</option>                                   
            </select>
        </span>                                
 </h4>
        </form>
        <table id="dyntable" class="table table-infinite table-bordered responsive table-autowidth">
            <thead>
                <tr class="main-header">
                    <th class="head0 report-itemlabel">Location</th>
                    <th class="head0 report-itemlabel">Item Name</th>
                    <th class="head0 report-itemlabel">Item Code</th>
                    <th class="head0 report-itemlabel">Item Group</th>
                    <th class="head0 report-itemlabel">Min. Stock </th>
                    <th class="head0 report-itemlabel">Max. Stock </th>
                    <th class="head0 report-itemlabel">Re-Order </th>
                    <th class="head0 report-stocklabel">Stock Qty.</th>
                    <th class="head0 report-stocklabel">Unit</th>

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
jQuery("#min_max").trigger('liszt:activate');
var headerOpt = {'Authorization':'Bearer {{ Auth::user()->auth_token }}','X-CSRF-TOKEN':'{{ csrf_token() }}'};
jQuery('#export-excel').on('click',function(){
    jQuery('.export_report').click();
});
 loadDataTable();
function loadDataTable(){
    
    if ( jQuery.fn.DataTable.isDataTable('#dyntable') ) {
        jQuery('#dyntable').DataTable().destroy();
    }

    var data = new FormData(document.getElementById('orderSearchForm'));
    var formValue = Object.fromEntries(data.entries());

    var table= jQuery('#dyntable').DataTable({
    "processing": true,
    "serverSide": true,
    "order": [[ 1, 'asc' ]],
    "scrollX":true,
    "sScrollX": "100%",
    "sScrollXInner": "110%",
    "bScrollCollapse": true,
    //"scrollX":true,
    //  "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],  
        pageLength : 25,
        dom: 'Blfrtip',
        buttons:
                [
                    {
                        extend:'excel',
                        filename: 'Stock Report',
                        title:"",
                        className: 'export_report d-none',
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
            url: "{{ route('listing-report') }}",
            type: "POST",
            headers: headerOpt,
            data : {                       
                    'min_max':formValue.min_max,                     
                },
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
        { data: 'location_name' ,name: 'locations.location_name' , },
        { data: 'item_name' ,name: 'items.item_name' , },
        { data: 'item_code' ,name: 'items.item_code' , },
        { data: 'item_group_name' ,name: 'item_groups.item_group_name' , },
        { data: 'min_stock_qty', name: 'items.min_stock_qty', },
        { data: 'max_stock_qty', name: 'items.max_stock_qty', },
        { data: 're_order_qty', name: 'items.re_order_qty', },
        { data: 'stock_qty' ,name: 'location_stock.stock_qty' ,},
        { data: 'unit_name' ,name: 'units.unit_name' , },

    ],

    rowCallback: function(row, data, index) {
            var stockQty = parseFloat(data.stock_qty.replace(/,/g, ''));  
            var minStockQty = parseFloat(data.min_stock_qty.replace(/,/g, '')); 
            var maxStockQty = parseFloat(data.max_stock_qty.replace(/,/g, '')); 


        /*   if(minStockQty != 0){
                if (stockQty < minStockQty) {
                    jQuery(row).find('td').eq(7).css('background', '#FF5B61');
                    jQuery(row).find('td').eq(1).css('background', '#FF5B61');
                }
            }
            if(maxStockQty != 0){
                if(stockQty > maxStockQty){
                    jQuery(row).find('td').eq(7).css('background', '#ffffc5');
                    jQuery(row).find('td').eq(1).css('background', '#ffffc5');
                }
            }*/

            if(minStockQty != 0 || maxStockQty != 0 ){
                if (stockQty < minStockQty) {
                    jQuery(row).find('td').eq(7).css('background', '#FF5B61');
                    jQuery(row).find('td').eq(1).css('background', '#FF5B61');
                }
                else if(stockQty > maxStockQty){
                    jQuery(row).find('td').eq(7).css('background', '#ffffc5');
                    jQuery(row).find('td').eq(1).css('background', '#ffffc5');
                }
            } 
        
        },

         initComplete: function () {
            // Exclude first column (index 0) from search
            initColumnSearch('#dyntable', []);
        },
    });
}


jQuery("#min_max").on("change", function(){
    loadDataTable();
});
});



</script>
@endsection
