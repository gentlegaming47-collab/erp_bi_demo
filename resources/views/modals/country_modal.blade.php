<!--Start country modal-->
<div aria-hidden="false" aria-labelledby="countryLabel" role="dialog" class="modal over-over-over hide fade in countrymodal" id="countryModal">
    <div class="modal-header">
        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
        <h3 id="countryModalLabel">Add Country</h3>
    </div>
    <div class="modal-body">
        <form id="commonCountryForm" class="stdform" method="post">
            @csrf
            <div class="row">
                @include('common_form_files.country')
            </div>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" id="commonCountryForm" type="submit" form="commonCountryForm" tabindex="0">Add</button>
        <button data-dismiss="modal" type="button" class="btn" tabindex="0">Close</button>
    </div>
</div>
<!--End country modal-->