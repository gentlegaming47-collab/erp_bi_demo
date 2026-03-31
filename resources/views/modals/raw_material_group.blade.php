<!--Start unit modal-->
<div aria-hidden="false" aria-labelledby="rawMaterialGroupLabel" role="dialog" class="modal over hide fade in" id="rawMaterialGroup">
    <div class="modal-header">
        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
        <h3 id="rawMaterialGroupLabel">Add Raw Material Group</h3>
    </div>
    <div class="modal-body">
        <form id="addrawMaterialGroupFormModal" class="stdform" method="post">
            @csrf
            <div class="row">
                <div class="par control-group">
                    <label class="control-label" for="raw_material_group_nm">Raw Material Group</label>
                    <div class="controls">
                        <input type="text" name="raw_material_group_nm" id="raw_material_group_nm" onkeyup="suggestRawMaterialGroup(event,this)" class="input-large auto-suggest" autocomplete="nope" autofocus/>

                        <div id="raw_material_group_nm_list" class="suggestion_list" ></div>
                    </div>
                </div>

              
            </div>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" id="addrawMaterialGroupFormModal" type="submit" form="addrawMaterialGroupFormModal" tabindex="0">Add</button>
        <button data-dismiss="modal" type="button" class="btn" tabindex="0">Close</button>
    </div>
</div>
<!--End unit modal-->