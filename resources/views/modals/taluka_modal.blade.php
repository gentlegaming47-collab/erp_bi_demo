<!-- Modals -->
@include('modals.state_modal')
@include('modals.city_modal')

<!--Start taluka modal-->
<div aria-hidden="false" aria-labelledby="talukaLabel" role="dialog" class="modal over-over-over-over hide fade in" id="talukaModal" style="z-index:1050; !important">
    <div class="modal-header">
        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
        <h3 id="talukaModalLabel">Add Taluka</h3>
    </div>
    <div class="modal-body" id="talukaBody">
        <form id="commonTalukaForm" class="stdform" method="post">
            @csrf
            <div class="row">
                    @include('common_form_files.taluka')
            </div>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" id="talukaButton" type="submit" form="commonTalukaForm" tabindex="0">Add</button>
        <button data-dismiss="modal" id="cancelTaluka" type="button" class="btn" tabindex="0">Close</button>
    </div>
</div>
<!--End city modal-->




