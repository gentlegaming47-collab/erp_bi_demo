@extends('layouts.app',['pageTitle' => 'Supplier PO'])

@section('header')
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>Supplier PO</li>
</ul>
@endsection

@section('content')
<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">
           <a href="{{ route('export-SupplierPo') }}" class="btn btn-inverse pre2">Export</a>
           @if(hasAccess("supplier_po","add"))
           <a href="{{ route('add-supplier_po') }}" class="btn btn-inverse">Add</a>
           @endif
        </div>
        <h4 class="widgettitle">Supplier PO</h4>
    </div>
    <div class="widgetcontent">
        <table id="dyntable" class="table table-infinite table-bordered responsive table-autowidth">
            <thead>
                <tr>
                    <th class="head0">Actions</th>
                    <th class="head0">PO No.</th>
                    <th class="head0">PO Date</th>
                    <th class="head1">Supplier</th>
                    <th class="head0">WO No.</th>
                    <th class="head0">WO Date</th>
                    <th class="head0">OA No.</th>
                    <th class="head0">OA Date</th>
                    <th class="head1">PO No.</th>
                    <th class="head0">PO Date</th>
                    <th class="head0">Part No.</th>
                    <th class="head0">Description</th>
                    <th class="head1">Material</th>
                    <th class="head0">PO Qty.</th>
                    <th class="head0">Weight</th>
                    <th class="head0">Rate</th>
                    <th class="head1">Rate Per</th>
                    <th class="head0">Amount</th>
                    <th class="head0">Del. Date</th>
                    <th class="head0">Remark</th>
                    <th class="head0">Modified By</th>
                    <th class="head1">Modified On</th>
                    <th class="head0">Created By</th>
                    <th class="head1">Created On</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>

jQuery(document).ready(function() {
var table= jQuery('#dyntable').DataTable({
"processing": true,
"serverSide": true,
"order": [[ 23, 'desc' ]],
"scrollX":true,
"fixedColumns":   {
				"leftColumns": 1,
			},

ajax: {
        url: "{{ route('listing-supplier_po') }}",
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
     {
        data: 'options',
        name: 'options',
        orderable: false,
        searchable: false,
    },
    { data: 'supplier_po_no' ,name: 'supplier_po.supplier_po_no' , },
    { data: 'supplier_po_date' ,name: 'supplier_po.supplier_po_date' , },
    { data: 'supplier_name' ,name: 'suppliers.supplier_name' ,},
    { data: 'work_order_no' ,name: 'work_order.work_order_no' ,},
    { data: 'work_order_date' ,name: 'work_order.work_order_date' , },
    { data: 'order_acceptance_no' ,name: 'customer_order_acceptance.order_acceptance_no' ,},
    { data: 'order_acceptance_date' ,name: 'customer_order_acceptance.order_acceptance_date' ,},
    { data: 'po_no' ,name: 'customer_order_acceptance.po_no' , },
    { data: 'po_date' ,name: 'customer_order_acceptance.po_date' , },
    { data: 'part_no' ,name: 'product.part_no' , },
    { data: 'description' ,name: 'coa_details.description' , },
    { data: 'material' ,name: 'coa_details.material' , },
    { data: 'po_qty' ,name: 'supplier_po_order_detail.po_qty' , },
    { data: null ,name:'coa_details.casting_wt_pc',
        render: function ( data, type, row, meta ) {
            return isNaN(Number(data['casting_wt_pc'])) ? 0 : Number(data['casting_wt_pc']).toFixed(3);
        },
    },
    { data: null ,name:'supplier_po_order_detail.rate',
        render: function ( data, type, row, meta ) {
            return isNaN(Number(data['rate'])) ? 0 : Number(data['rate']).toFixed(2);
        },
    },
    { data: 'name' ,name:'rate_type.name',},
    { data: null ,name:'supplier_po_order_detail.amount',
        render: function ( data, type, row, meta ) {
            return isNaN(Number(data['amount'])) ? 0 : Number(data['amount']).toFixed(2);
        },
    },
    { data: 'del_date' ,name: 'supplier_po_order_detail.del_date' , },
    { data: 'remark' ,name: 'supplier_po_order_detail.remark' , },
    { data: 'last_by_user_id',name: 'supplier_po.last_by_user_id' , },
    { data: 'last_on',name: 'supplier_po.last_on' , },
    { data: 'created_by_user_id',name: 'supplier_po.created_by_user_id' ,},
    { data: 'created_on',name: 'supplier_po.created_on' , },
]
});
jQuery('#dyntable tbody').on( 'click', '#del_a', function () {
    var data = table.row( jQuery(this).parents('tr') ).data();

    jConfirm('Are You Sure, You Want <lw-c>to</lw-c> Delete ?', 'Confirmation', function(r) {
        if(r === true){
            jQuery.ajax({
				url: "{{ route('remove-supplier_po') }}",
				type: 'GET',
				data: "id="+data["id"],
                dataType: 'json',
                processData: false,
				success: function (data) {
                    if(data.response_code == 1){
			            toastSuccess(data.response_message);
                        table.row(jQuery(this)).draw(false);
                    }else{
			            toastError(data.response_message);
                    }
				},
                error: function (jqXHR, textStatus, errorThrown){
                    if(jqXHR.status == 401){
                        toastError(jqXHR.statusText);
                    }else{
                       toastError('Somthing went wrong!');
                    }
                    console.log(JSON.parse(jqXHR.responseText));
                }
		});
        }
    });
});

});
</script>
@endsection
