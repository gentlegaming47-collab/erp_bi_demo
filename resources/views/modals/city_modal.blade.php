<!-- Modals -->

{{-- @include('modals.state_modal') --}}

<!-- End Modals -->

<!--Start city modal-->
<div aria-hidden="false" aria-labelledby="cityLabel" role="dialog" class="modal over hide fade in city" id="cityModal">
    <div class="modal-header">
        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
        <h3 id="cityModalLabel">Add District</h3>
    </div>
    <div class="modal-body">
        <form id="commonDistrictForm" class="stdform" method="post">
            @csrf
            <div class="row">
                    @include('common_form_files.district')
            </div>
        </form>
    </div>
 


    <div class="modal-footer">
            <button class="btn btn-primary" id="addCityModal" type="submit" form="commonDistrictForm" tabindex="0">Add</button>
            <button data-dismiss="modal" type="button" class="btn" tabindex="0">Close</button>
        </div>

</div>
<!--End city modal-->