@extends('layouts.app',['pageTitle' => 'Material Requirement'])

@section('header')
<style>
    .dataTables_filter {
    top: 35px;
    }
</style>
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>Material Requirement</li>
</ul>
@endsection

@section('content')
<div class="widgetbox">
    <div class="headtitle">
       
        <div class="btn-group">
            <a href="javascript:void(0)" class="btn btn-inverse pre2" id="export-excel">Export</a>
        </div>


        <h4 class="widgettitle">Material Requirement</h4>
    </div>
    <div class="widgetcontent overflow-scroll">
        <form id="orderSearchForm" name="orderSearchForm" class="stdform">
          
            <div class="row">
                <div class="span-6">
                    <div class="par control-group form-control">
                            <label class="control-label" for="sales_order_id">SO No. </label>
                        <div class="controls">
                                <span class="formwrapper"> 
                                    <select name="sales_order_id" id="sales_order_id" class="chzn-select">
                                        

                                        
                                        @if(getAllSalesOrder()->isEmpty())                       
                                            <option value="">Select SO No.</option>              
                                        @else 
                                            <option value="">All</option>                     @endif 

                                            @forelse (getAllSalesOrder() as $sales_data)
                                            
                                            {{-- <option value="{{ $sales_data->so_id }}">{{ $sales_data->so_number}}</option> --}}
                                            <option value="{{ $sales_data->id }}">{{ $sales_data->so_number}}</option>
                                            @empty
                                        @endforelse
                                    </select>
                                </span>
                        </div>
                    </div>
                </div>                           
            </div>
           
          

        </form>

        <table id="dyntable" class="table table-infinite table-bordered responsive table-autowidth">
            <thead>
                <tr class="main-header">
                    <th class="head1">Item Name</th>
                    <th class="head0">Item Code</th>
                    <th class="head0">Pend. SO Qty.</th>
                    <th class="head1">Stock Qty.</th>
                    <th class="head0">Pend. Mat. Rec. Qty.</th>
                    <th class="head0">Need Qty.</th>
                    <th class="head1">Unit</th>
                    
                </tr>
            </thead>
        </table>
        <div class="note-text">Note: To search across multiple columns, add a space between words.</div>
    </div>
</div>
@endsection

@section('scripts')



{{-- <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script> --}}


<script>
var table = "";

jQuery(document).ready(function() {
        jQuery("#sales_order_id").trigger('liszt:activate');

        var headerOpt = {'Authorization':'Bearer {{ Auth::user()->auth_token }}','X-CSRF-TOKEN':'{{ csrf_token() }}'};
        jQuery('#export-excel').on('click',function(){
            jQuery('.export_material_requirement').click();
        });

        var calcDataTableHeight = function () {
        return jQuery(window).height() * 55 / 100;
        };


        loadDataTable();

    function loadDataTable(){

            if ( jQuery.fn.DataTable.isDataTable('#dyntable') ) {
                jQuery('#dyntable').DataTable().destroy();
            }

            var data = new FormData(document.getElementById('orderSearchForm'));
            var formValue = Object.fromEntries(data.entries());

            table= jQuery('#dyntable').DataTable({
                "processing": true,
                "serverSide": false,
                "scrollX" : true,
                "sScrollX": "100%",
                "sScrollXInner": "110%",
                "bScrollCollapse": true,
                "sScrollXInner": "110%",
                "scrollX":true,
            
            // "lengthMenu": [10, 25, 50, "All"],
            pageLength : 25,
            dom: 'Blfrtip',

             buttons:
                [
                    {
                        extend:'excel',
                        filename: 'Material Requirement',
                        title:"",
                        className: 'export_material_requirement d-none',
                        exportOptions: {
                            // columns: function(idx, data, node) {
                            //     return idx !== 0 && table.column(idx).visible();
                            // },
                            //columns: ':not(:eq(0))',
                            modifier: {
                                page: 'all'
                            }
                        },
                        action: newexportaction

                    }
                ],
           
      
          
            "ajax": {
                    url: "{{ route('listing-material_requirement') }}",
                    type: "POST",
                    headers: headerOpt,
                    data : {                       
                        'sales_order_id':formValue.sales_order_id,                     
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
                { data: 'item_name' ,name: 'items.item_name' ,},
                { data: 'item_code' ,name: 'items.item_code' ,},
                { data: 'pending_so_qty' ,name: 'pending_so_qty' ,},
                { data: 'stock_qty' ,name: 'stock_qty' ,},
                { data: 'pend_mat_rec_qty' ,name: 'pend_mat_rec_qty' ,},
                { data: 'need_qty' ,name: 'need_qty' ,},
                { data: 'unit_name' ,name: 'units.unit_name' ,},                
            ],
            initComplete: function () {
                // Exclude first column (index 0) from search
                initColumnSearch('#dyntable', []);
            }
            });

    }


jQuery("#sales_order_id").on("change", function(){
    loadDataTable();
});

// jQuery(window).resize(function () {
//     var oSettings = table.fnSettings();
//     oSettings.oScroll.sY = calcDataTableHeight();
//     table.fnDraw();
// });



jQuery("#orderSearchForm").validate({

    
    submitHandler: function(form) {

        var searchForm = jQuery('#orderSearchForm');

        loadDataTable();

    }

});


jQuery('#reset-order-data').on('click',function(){
    var searchForm = jQuery("#orderSearchForm");

 
    
    searchForm.find('#sales_order_id').val('').trigger('liszt:updated');

  
    loadDataTable();
});


}); 

</script>
@endsection