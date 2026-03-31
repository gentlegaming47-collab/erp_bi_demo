@extends('layouts.app',['pageTitle' => 'Supplier Inward'])

@section('header')
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>Supplier Inward</li>
</ul>
@endsection

@section('content')
<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">
           <a href="{{ route('export-SupplierInwardGrn') }}" class="btn btn-inverse pre2">Export</a>
           @if(hasAccess("supplier_inward","add"))
           <a href="{{ route('add-supplier_inward') }}" class="btn btn-inverse">Add</a>
           @endif
        </div>
        <h4 class="widgettitle">Supplier Inward</h4>
    </div>
    <div class="widgetcontent">
        <table id="dyntable" class="table table-infinite table-bordered responsive table-autowidth">
            <thead>
                <tr>
                    <th class="head0">Actions</th>
                    <th class="head1">Company</th>
                    <th class="head0">GRN Date.</th>
                    <th class="head0">GRN No.</th>
                    <th class="head1">Supplier</th>
                    <th class="head0">Supplier PO No.</th>
                    <th class="head0">WO No.</th>
                    <th class="head0">OA No.</th>
                    <th class="head1">PO No.</th>
                    <th class="head0">Part No.</th>
                    <th class="head1">Material</th>
                    <th class="head1">Pend. PO Qty.</th>
                    <th class="head0">Inward Qty.</th>
                    <th class="head0">Remark</th>
                    <th class="head1">Total Qty.</th>
                    <th class="head0">Total Wt.</th>
                    <th class="head0">Sp. Note</th>
                    <th class="head0">Modified by</th>
                    <th class="head1">Modified on</th>
                    <th class="head0">Created by</th>
                    <th class="head1">Created on</th>
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
"order": [[ 20, 'desc' ]],
"scrollX":true,
"fixedColumns":   {
				"leftColumns": 1,
			},

ajax: {
        url: "{{ route('listing-supplier_inward') }}",
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
    { data: 'company_unit_name' ,name: 'company_unit.company_unit_name' ,},
    { data: 'supplier_inward_date' ,name: 'supplier_inward_grn.supplier_inward_date' , },
    { data: 'supplier_inward_no' ,name: 'supplier_inward_grn.supplier_inward_no' , },
    { data: 'supplier_name' ,name: 'suppliers.supplier_name' ,},

    { data: 'supplier_po_no' ,name: 'supplier_po.supplier_po_no' ,},

    { data: 'work_order_no' ,name: 'work_order.work_order_no' ,},

    { data: 'order_acceptance_no' ,name: 'customer_order_acceptance.order_acceptance_no' ,},

    { data: 'po_no' ,name: 'customer_order_acceptance.po_no' , },

    { data: 'part_no' ,name: 'product.part_no' ,},

    { data: 'material' ,name: 'coa_details.material' , },

    { data: 'pend_po_qty' ,name: 'pend_po_qty' ,orderable: false,searchable: false, },

    { data: 'inward_qty' ,name: 'supplier_inward_details_grn.inward_qty' , },

    { data: 'remark' ,name: 'supplier_inward_details_grn.remark' , },

    { data: 'total_qty' ,name:'supplier_inward_grn.total_qty',},
    { data: null ,name:'supplier_inward_grn.total_weight',
        render: function ( data, type, row, meta ) {
            return isNaN(Number(data['total_weight'])) ? 0 : Number(data['total_weight']).toFixed(3);
        },
    },
    { data: 'special_note' ,name:'supplier_inward_grn.special_note',},
    { data: 'last_by_user_id',name: 'supplier_inward_grn.last_by_user_id' , },
    { data: 'last_on',name: 'supplier_inward_grn.last_on' , },
    { data: 'created_by_user_id',name: 'supplier_inward_grn.created_by_user_id' ,},
    { data: 'created_on',name: 'supplier_inward_grn.created_on' , },
]
});
jQuery('#dyntable tbody').on( 'click', '#del_a', function () {
    var data = table.row( jQuery(this).parents('tr') ).data();

    jConfirm('Are You Sure, You Want <lw-c>to</lw-c> Delete ?', 'Confirmation', function(r) {
        if(r === true){
            jQuery.ajax({
				url: "{{ route('remove-supplier_inward') }}",
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
