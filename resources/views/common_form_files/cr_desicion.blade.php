   
<div class="row">
        <div class="span-6">
                <div class="par control-group form-control">
                    <label class="control-label" for="date">Date </label>
                        <div class="controls"> <span class="formwrapper">
                             <input name="date" id="date" class="input-large trans-date-picker no-fill" />
                            </span> </div>
            </div>
        </div>

        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label"></label>
                    <span class="formwrapper radioClass">
                        <input type="radio" name="cr_scrap" value="1" onchange="changeGrNValue(this)" checked/> Scrap&nbsp; &nbsp;
                        <input type="radio" name="cr_scrap" value="2" onchange="changeGrNValue(this)"/> Store &nbsp; &nbsp;
                    </span>                    
            </div>
        </div>
</div>
    


    
    
    
    <div class="widgetbox-inverse">
            <div class="headtitle">
                <h4 class="widgettitle">Items List</h4>
            </div>
            <div class="widgetcontent overflow-scroll">
                <table class="table table-bordered responsive" id="CRDecisionTable">
                    <thead>
                        <tr>
                            <th>Action</th>                        
                            <th>CR No.</th>
                            <th>CR Dae</th>
                            <th>Customer</th>
                            <th>Item Name</th>
                            <th>Item Code</th>
                            <th>Ref. Qty</th>
                            <th>Pending Decision Qty.</th>
                            <th>Decision Qty.</th>                            
                        </tr>
                    </thead>
                    <tbody>
    
    
                    </tbody>
                    <tfoot>
                        <tr class="total_tr">
                            {{-- <td colspan="8" ></td> --}}
                            <td class="grnqtysum" name="grn_total_qty"></td>                     
                            <td class="amountsum" name="grn_total_amount">    
                            </tr>
    
                    </tfoot>
    
                </table><br>
            </div>
        </div>
    


    
    <script>
    var getItem = [<?php echo json_encode(getFittingItem()); ?>];
     </script>
    
     <script src="{{ asset('js/view/delivery_challan.js?ver='.getJsVersion()) }}"></script>

     
    