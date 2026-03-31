<!--Start Dealer modal-->
<div aria-hidden="false" aria-labelledby="dealerLabel" role="dialog" class="modal modal-wide over over over  hide fade in dealermodal" id="dealerModal">
    <div class="modal-header">
        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
        <h3 id="dealerModalLabel">Add Dealer</h3>
    </div>
    <div class="modal-body">
        <form id="commonDealerForm" class="stdform" method="post">
        <input type="hidden" id="dealer_modal_id" value="dealer"> 
            @csrf
            <div class="row">
                @include('common_form_files.dealer')
            </div>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" id="commonDealerForm" type="submit" form="commonDealerForm" tabindex="0">Add</button>
        <button data-dismiss="modal" type="button" class="btn" tabindex="0">Close</button>
    </div>
</div>
<!--End Dealer modal-->