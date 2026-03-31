
<!-- Start Country Modal -->

@include('modals.country_modal')

<!-- End Country Modal -->

<!--Start state modal-->
<div aria-hidden="false" aria-labelledby="stateLabel" role="dialog" class="modal over-over hide fade in change_state_modal" id="stateModal">
    <div class="modal-header">
        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
        <h3 id="stateModalLabel">Add State</h3>
    </div>
    <div class="modal-body">
        <form id="commonStateForm" class="stdform" method="post">
            @csrf
            <div class="row">
                    @include('common_form_files.state')
            </div>
        </form>
    </div>

    <div class="modal-footer">
        <button class="btn btn-primary" id="addStateModal" type="submit" form="commonStateForm" tabindex="0">Add</button>
        <button data-dismiss="modal" id="cancelState" type="button" class="btn" tabindex="0">Close</button>
    </div>
</div>
<!--End state modal-->