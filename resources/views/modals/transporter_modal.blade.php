<!-- Modals -->

{{-- @include('modals.state_modal') --}}

<!-- End Modals -->

<!--Start city modal-->
<div aria-hidden="false" aria-labelledby="cityLabel" role="dialog" class="modal modal-wide over hide fade in city" id="transportModal">
    <div class="modal-header">
        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
        <h3 id="transporterModalLabel">Add Transporter</h3>
    </div>
    <div class="modal-body">
        <form id="commonTransporterForm" class="stdform" method="post">
            @csrf
                    @include('common_form_files.transporter')
        </form>
    </div>
 


    <div class="modal-footer">
            <button class="btn btn-primary" id="transportModal" type="submit" form="commonTransporterForm" tabindex="0">Add</button>
            <button data-dismiss="modal" type="button" class="btn" tabindex="0">Close</button>
    </div>

</div>
<!--End city modal-->