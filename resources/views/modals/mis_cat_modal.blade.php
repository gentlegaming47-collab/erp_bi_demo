<!--Start MIS modal-->
<div aria-hidden="false" aria-labelledby="misLabel" role="dialog" class="modal over-over-over hide fade in countrymodal" id="MisCatModal">
    <div class="modal-header">
        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
        <h3 id="MisCatModalLable">Add MIS Category</h3>
    </div>
    <div class="modal-body">
        <form id="commonMisCategoryForm" class="stdform" method="post">
            @csrf
            <div class="row">
                @include('common_form_files.mis_category')
            </div>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" id="commonMisCategoryForm" type="submit" form="commonMisCategoryForm" tabindex="0">Add</button>
        <button data-dismiss="modal" type="button" class="btn" tabindex="0">Close</button>
    </div>
</div>
<!--End MIS modal-->