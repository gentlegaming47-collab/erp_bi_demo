<!--Start Item modal-->
<div aria-hidden="false" aria-labelledby="itemGroupLabel" role="dialog" class="modal over-over hide fade in" id="itemGroupModal">
    <div class="modal-header">
        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
        <h3 id="itemGroupModalLabel">Add Item Group</h3>
    </div>
    <div class="modal-body">
        <form id="commonItmeGroupForm" class="stdform" method="post">
            @csrf
            <div class="row">
                    @include('common_form_files.item-group')
            </div>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" id="commonItmeGroupForm" type="submit" form="commonItmeGroupForm" tabindex="0">Add</button>
        <button data-dismiss="modal" type="button" class="btn" tabindex="0">Close</button>
    </div>
</div>
<!--End Item modal-->