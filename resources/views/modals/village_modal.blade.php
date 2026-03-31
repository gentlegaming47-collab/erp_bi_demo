<!-- Modals -->
@include('modals.state_modal')



<!--Start taluka modal-->
<div aria-hidden="false" aria-labelledby="VillageLabel" role="dialog" class="modal over-over-over-over-over  hide fade in" id="VillageModal">
{{-- <div aria-hidden="false" aria-labelledby="VillageLabel" role="dialog" class="modal over-over-over-over-over  hide fade in" id="VillageModal" style="z-index:1041 !important"> --}}
    <div class="modal-header">
        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
        <h3 id="VillageModalLabel">Add Village</h3>
    </div>
    <div class="modal-body" id="villageBody">
        <form id="commonVillageForm" class="stdform" method="post">
            @csrf
            <div class="row">
                @include('common_form_files.village')
            </div>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" id="commonVillageForm" type="submit" form="commonVillageForm" tabindex="0">Add</button>
        <button data-dismiss="modal" type="button" class="btn" tabindex="0">Close</button>
    </div>
</div>
<!--End city modal-->




