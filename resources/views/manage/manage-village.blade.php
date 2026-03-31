@extends('layouts.app',['pageTitle' => 'Village'])

@section('header')
<style>
    .dataTables_filter {
    top: 35px;
    }
</style>
<ul class="breadcrumbs">
    <li><a href="{{ route('dashboard') }}"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
    <li>Village</li>
</ul>
@endsection

@section('content')
<div class="widgetbox">
    <div class="headtitle">
        <div class="btn-group">
                {{-- <a href="javascript:void(0)" class="btn btn-inverse pre2" id="export-excel">Export</a> --}}
                <a href="javascript:void(0)" class="btn btn-inverse pre2" id="export-excel">Export</a>

                {{-- <a href="{{ route('export-Village') }}" class="btn btn-inverse pre2">Export</a> --}}
            {{-- <a href="javascript:void(0)" class="btn btn-inverse pre2" id="export-excel">Export</a> --}}
            {{-- <a href="{{ route('export-Village') }}" class="btn btn-inverse pre2">Export</a> --}}
           @if(hasAccess("village","add"))
           <a href="{{ route('add-village') }}" class="btn btn-inverse">Add</a>
           @endif
          

        </div>
        <h4 class="widgettitle">Village</h4>
    </div>
    <div class="widgetcontent overflow-scroll">
        <table id="dyntable" class="table table-infinite table-bordered responsive table-autowidth">
            <thead>
                <tr class="main-header">
                    <th class="head0">Actions</th>
                    <th class="head1">Village</th>
                    <th class="head0">Pin Code</th>                    
                    <th class="head0">Taluka</th>
                    <th class="head1">District</th>
                    <th class="head0">State</th>
                    <th class="head1">Country</th>
                    <th class="head1">Modified by</th>
                    <th class="head0">Modified on</th>
                    <th class="head1">Created by</th>
                    <th class="head0">Created on</th>
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
    jQuery('.export_village').click();
});
var table= jQuery('#dyntable').DataTable({

"processing": true,
"serverSide": true,
"order": [[ 1, 'asc' ]],
"scrollX":true,
"sScrollX": "100%",
"sScrollXInner": "110%",
"bScrollCollapse": true,
// "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
 pageLength : 25,
 dom: 'Blfrtip',
 buttons:
            [
                {
                    extend:'excel',
                    filename: 'Village',
					title:"",
                    className: 'export_village d-none',
                    exportOptions: {
                        modifier: {
                            page: 'all'
                        }
                    },
                    action: newexportaction
                }
            ],
ajax: {
        url: "{{ route('listing-village') }}",
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
     {
        data: 'options',
        name: 'options',
        orderable: false,
        searchable: false,
    },
    { data: 'village_name' ,name: 'villages.village_name', },
    { data: 'default_pincode' ,name: 'villages.default_pincode', },
    { data: 'taluka_name' ,name: 'talukas.taluka_name', },
    { data: 'district_name' ,name: 'districts.district_name', },    
    { data: 'state_name' ,name: 'states.state_name', },
    { data: 'country_name' ,name: 'countries.country_name', },
    { data: 'last_by_user_id',name: 'last_by_user_id',  },
    { data: 'last_on',name: 'villages.last_on',  },
    { data: 'created_by_user_id',name: 'created_by_user_id', },
    { data: 'created_on',name: 'villages.created_on',  },
],
initComplete: function () {
    // Exclude first column (index 0) from search
    initColumnSearch('#dyntable', [0]);
}
});

jQuery('#dyntable tbody').on( 'click', '#del_a', function () {
    var data = table.row( jQuery(this).parents('tr') ).data();

    jConfirm('Are you Sure, You Want <lw-c>to</lw-c> Delete ?', 'Confirmation', function(r) {
        if(r === true){
            jQuery.ajax({
				url: "{{ route('remove-village') }}",
				type: 'GET',
				data: "id="+data["id"],
                headers: headerOpt,
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
                        // toastError(jqXHR.statusText);
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

// To Export All Data in Excel 
// function newexportaction(e, dt, button, config) {
  
//          var self = this;
//          var oldStart = dt.settings()[0]._iDisplayStart;
//          dt.one('preXhr', function (e, s, data) {
//              // Just this once, load all data from the server...
//              data.start = 0;
//              data.length = -1;
//              dt.one('preDraw', function (e, settings) {
//                  // Call the original action function
//                  if (button[0].className.indexOf('buttons-copy') >= 0) {
//                      jQuery.fn.dataTable.ext.buttons.copyHtml5.action.call(self, e, dt, button, config);
//                  } else if (button[0].className.indexOf('buttons-excel') >= 0) {
//                          jQuery.fn.dataTable.ext.buttons.excelHtml5.available(dt, config) ?
//                          jQuery.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config) :
//                          jQuery.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
//                  } 
//                  dt.one('preXhr', function (e, s, data) {
//                      // DataTables thinks the first item displayed is index 0, but we're not drawing that.
//                      // Set the property to what it was before exporting.
//                      settings._iDisplayStart = oldStart;
//                      data.start = oldStart;
//                  });
//                  // Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
//                  setTimeout(dt.ajax.reload, 0);
//                  // Prevent rendering of the full data to the DOM
//                  return false;
//              });
//          });
//          // Requery the server with the new one-time export settings
//          dt.ajax.reload();
//      }
// // End Export All Data In excel
// });


function newexportaction(e, dt, button, config) {
    var self = this;
    var oldStart = dt.settings()[0]._iDisplayStart;  // Get the original start value
    var chunkSize = 5000;  // Define the chunk size for export
    var totalRecords = dt.page.info().recordsDisplay;  // Total number of records currently visible
    var currentStart = 0;
    var allData = [];

    // Function to fetch data in chunks
    function fetchChunk() {
        dt.one('preXhr', function (e, settings, data) {
            data.start = currentStart;  // Set start for pagination
            data.length = chunkSize;    // Set the chunk size for the request
            dt.one('preDraw', function () {
                var chunkData = dt.rows().data().toArray(); // Get the current chunk of data
                allData = allData.concat(chunkData); // Append to the full data set
                currentStart += chunkSize; // Move to the next chunk

                // Check if all data has been fetched
                if (currentStart < totalRecords) {
                    fetchChunk();  // Continue fetching the next chunk
                } else {
                    // Once all data is fetched, proceed to export
                    exportData(allData);
                }
            });
        });

        dt.ajax.reload();  // Trigger an AJAX reload to fetch the data for this chunk
    }

    // Start fetching the data in chunks
    fetchChunk();
}



});



</script>
@endsection
