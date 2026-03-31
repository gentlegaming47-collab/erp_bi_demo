@extends('layouts.app',['pageTitle' => 'Expire Dealer Report'])

@section('header')
<style>
    .dataTables_filter {
    top: 35px;
    }
</style>
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>Expire Dealer Report</li>
</ul>
@endsection

@section('content')
<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">
            <a href="javascript:void(0)" class="btn btn-inverse pre2" id="export-excel">Export</a>
        </div>
        <h4 class="widgettitle">Expire Dealer Report</h4>
    </div>
    <div class="widgetcontent overflow-scroll">
        <table id="dyntable" class="table table-infinite table-bordered responsive table-autowidth">
            <thead>
                <tr class="main-header">
                    <th class="head1">Dealer Name</th>
                    <th class="head1">Agreement Start Date</th>
                    <th class="head1">Agreement End Date</th>                    
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
    jQuery('.export_expire_dealer').click();
});

var table= jQuery('#dyntable').DataTable({

    "processing": true,
    "order": [[ 1, 'asc' ]],

    "serverSide": true,
    "scrollX" : true,
    "sScrollX": "100%",
    "sScrollXInner": "110%",
    "bScrollCollapse": true,
    "order": [[ 0, 'asc' ]],
    // "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],  
    pageLength : 25,
    dom: 'Blfrtip',
    buttons:
        [
            {
                extend:'excel',
                filename: 'Expire Dealer Report',
                title:"",
                className: 'export_expire_dealer d-none',
                exportOptions: {
                        modifier: {
                            page: 'all'
                        }
                },
                action: newexportaction
            }
        ],
        ajax: {
                url: "{{ route('listing-expire_dealer_report') }}",
                type: "POST",
                headers: headerOpt,
                error: function (jqXHR, textStatus, errorThrown){
                    jQuery('#dyntable_processing').hide();
                    if(jqXHR.status == 401){
                        jAlert(jqXHR.statusText);
                    }else{
                        jAlert('Somthing went wrong!');
                    }
                    console.log(JSON.parse(jqXHR.responseText));
                }
        },

        columns: [
        
            { data: 'dealer_name', name: 'dealers.dealer_name', },
            { data: 'agreement_start_date', name: 'dealer_agreement.agreement_start_date', },
            { data: 'agreement_end_date', name: 'dealer_agreement.agreement_end_date', },
        ],
        initComplete: function () {
            // Exclude first column (index 0) from search
            initColumnSearch('#dyntable', []);
        }
});


});
</script>
@endsection
