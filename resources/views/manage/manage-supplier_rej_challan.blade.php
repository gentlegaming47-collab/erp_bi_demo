@extends('layouts.app',['pageTitle' => 'Supplier Return Challan'])

@section('header')
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>Supplier Return Challan</li>
</ul>
@endsection

@section('content')
<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">
                <a href="{{ route('export-SupplierRejChallan') }}" class="btn btn-inverse pre2">Export</a>
           @if(hasAccess("supplier_rej_challan","add"))
           <a href="{{ route('add-supplier_rej_challan') }}" class="btn btn-inverse">Add</a>
           @endif
        </div>
        <h4 class="widgettitle">Supplier Return Challan</h4>
    </div>
    <div class="widgetcontent">
        <table id="dyntable" class="table table-infinite table-bordered responsive table-autowidth">
            <thead>
                <tr>
                    <th class="head1">Actions</th>
                    <th class="head0">DC No.</th>
                    <th class="head0">DC Date</th>
                    <th class="head0" style="display:none;"></th>
                    <th calss="head1">GRN No.</th>
                    <th calss="head1">Date</th>
                    <th class="head0">Company</th>
                    <th class="head0">Supplier</th>
                    <th class="head0">Part No.</th>
                    <th class="head0">Material</th>
                    <th class="head0">Description</th>
                    <th class="head0">DC Qty.</th>
                    <th class="head0">Pend Qty.</th>
                    <th class="head0">Total Qty.</th>
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
var headerOpt = {'Authorization':'Bearer {{ Auth::user()->auth_token }}','X-CSRF-TOKEN':'{{ csrf_token() }}'};
var table= jQuery('#dyntable').DataTable({
"processing": true,
"serverSide": true,
// "order": [[ 18, 'desc' ]],
"order": [[ 2, 'desc' ],[ 3, 'desc' ]],

"scrollX":true,
"fixedColumns":   {
				"leftColumns": 1,
			},

ajax: {
        url: "{{ route('listing-supplier_rej_challan') }}",
        type: "POST",
        headers: headerOpt,
        error: function (jqXHR, textStatus, errorThrown){
            jQuery('#dyntable_processing').hide();
            if(jqXHR.status == 401){
                // toastError(jqXHR.statusText);
                jAlert(jqXHR.statusText);
            }else{
               // toastError('Somthing went wrong!');
                jAlert('Somthing went wrong!');
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

    { data: 'supplier_dc_no.' ,name: 'supplier_rejection_challan.supplier_dc_no' , },
    { data: 'supplier_dc_date' ,name: 'supplier_rejection_challan.supplier_dc_date' ,},
    { data: 'src_sequence' ,name: 'supplier_rejection_challan.src_sequence' , visible:false},
    { data: 'supplier_inward_no.' ,name: 'supplier_inward_grn.supplier_inward_no' , },

    { data: 'supplier_inward_date' ,name: 'supplier_inward_grn.supplier_inward_date' , },
    { data: 'company_unit_name' ,name: 'company_unit.company_unit_name' , },
    { data: 'supplier_name' ,name: 'suppliers.supplier_name' ,},

    { data: 'part_no' ,name: 'product.part_no' ,},

    { data: 'material' ,name: 'coa_details.material' , },

    { data: 'remark' ,name: 'supplier_rejection_challan_details.remark' ,},
    { data: 'rejection_qty' ,name: 'supplier_rejection_challan_details.dc_qty' , },
    { data: 'pend_dc_qty' ,name: 'pend_dc_qty' , orderable: false,searchable: false,},
    { data: 'total_qty' ,name: 'supplier_rejection_challan.total_qty' , },
    // { data: 'total_weight' ,name: 'supplier_rejection_challan.total_weight' , },

    { data: null ,name:'supplier_rejection_challan.total_weight',
        render: function ( data, type, row, meta ) {
            return isNaN(Number(data['total_weight'])) ? 0 : Number(data['total_weight']).toFixed(3);
        },
    },

    { data: 'special_note' ,name: 'supplier_rejection_challan.special_note' ,},
    { data: 'last_by_user_id',name: 'supplier_rejection_challan.last_by_user_id' ,  },
    { data: 'last_on',name: 'supplier_rejection_challan.last_on' ,  },
    { data: 'created_by_user_id',name: 'supplier_rejection_challan.created_by_user_id' ,},
    { data: 'created_on',name: 'supplier_rejection_challan.created_on' , },

]
});


jQuery('#dyntable tbody').on( 'click', '#del_a', function () {
    var data = table.row( jQuery(this).parents('tr') ).data();

    jConfirm('Are You Sure, You Want <lw-c>to</lw-c> Delete ?', 'Confirmation', function(r) {
        if(r === true){
            jQuery.ajax({
				url: "{{ route('remove-supplier_rej_challan') }}",
				type: 'GET',
				data: "id="+data["id"],
                dataType: 'json',
                processData: false,
				success: function (data) {
                    if(data.response_code == 1){
			            // toastSuccess(data.response_message);
                        jAlert(data.response_message);
                        table.row(jQuery(this)).draw(false);
                    }else{
			            // toastError(data.response_message);
                        jAlert(data.response_message);
                    }
				},
                error: function (jqXHR, textStatus, errorThrown){
                    if(jqXHR.status == 401){
                        // toastError(jqXHR.statusText);/
                        jAlert(jqXHR.statusText);
                    }else{
                       // toastError('Somthing went wrong!');
                        jAlert('Somthing went wrong!');
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
