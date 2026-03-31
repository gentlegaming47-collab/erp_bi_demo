   
    <div class="row">
        <div class="span-6">
            <div class="par control-group form-control">
                <label class="control-label"></label>
                    <span class="formwrapper radioClass">
                        <input type="radio" name="replacement_fix_id" id="replacement_fix_id" value="1" checked/> Store &nbsp; &nbsp;
                        <input type="radio" name="replacement_fix_id" id="replacement_fix_id" value="2"/> Scrap  &nbsp; &nbsp;
                        
                    </span>
                
            </div>
        </div>
    </div>        
    
   <div class="divider15"> </div>
    
    
    <div class="widgetbox-inverse">
            <div class="headtitle">
                <h4 class="widgettitle">Customer Replacement SO Mapping Details <sup class="astric">*</sup></h4>
            </div>
            {{-- <div class="widgetcontent overflow-scroll"> --}}
            <div class="widgetcontent" style="overflow-x:scroll;overflow:inherit;">
                <table class="table table-bordered responsive remove-reset-filter" id="replacementItemTable">
                    <thead>
                        <tr>
                            <th><input type="checkbox" name="checkall-so_mapping" class="simple-check" id="checkall-so_mapping"/></th>                        
                            <th>Sr. No.</th>                        
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Item</th>
                            <th>Item Detail Name</th>
                            <th>Item Code</th>                            
                            <th>Item Group</th>                            
                            <th>Pend. Map. Qty.</th>                            
                            <th>Item Detail Qty.</th>                            
                            <th>Decision Qty.</th>                            
                            <th>Unit</th>                            
                                                     
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="centeralign" id="replacementItemTable">
                            <td colspan="11">Customer Replacement SO Mapping Details Not Available</td>
                        </tr>
    
                    </tbody>
                   
    
                </table><br>
                {{-- <button class="btn btn-primary" type="button" id="addPart" onclick="addPartDetail()">Add</button> --}}
            </div>
        </div>
    
 
    
@section('scripts')
    <script type="text/javascript" src="{{ asset('js/view/replacement_item_decision.js?ver='.getJsVersion()) }}"></script>
@endsection


     
    