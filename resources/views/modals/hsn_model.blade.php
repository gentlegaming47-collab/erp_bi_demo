<!--Start HSN modal-->
<div aria-hidden="false" aria-labelledby="HSNLabel" role="dialog" class="modal over-over hide fade in" id="HSNModal">
        <div class="modal-header">
            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
            <h3 id="HSNlLabel">Add HSN Code</h3>
        </div>
        <div class="modal-body">
            <form id="commonHSNCodeForm" class="stdform" method="post">
                @csrf
                <div class="row">
                        @include('common_form_files.hsn_code')
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-primary" id="commonHSNCodeForm" type="submit" form="commonHSNCodeForm" tabindex="0">Add</button>
            <button data-dismiss="modal" type="button" class="btn" tabindex="0">Close</button>
        </div>
    </div>
    <!--End HSN modal-->