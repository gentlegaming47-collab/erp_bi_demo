<!--Start unit modal-->
<div aria-hidden="false" aria-labelledby="unitLabel" role="dialog" class="modal over hide fade in" id="unitModal">
    <div class="modal-header">
        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
        <h3 id="unitModalLabel">Add Unit</h3>
    </div>
    <div class="modal-body">
        <form id="commonUnitForm" class="stdform" method="post">
            @csrf
            <div class="row">
                @include('common_form_files.unit')
            </div>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" id="commonUnitForm" type="submit" form="commonUnitForm" tabindex="0">Add</button>
        <button data-dismiss="modal" type="button" class="btn" tabindex="0">Close</button>
    </div>
</div>
<!--End unit modal-->