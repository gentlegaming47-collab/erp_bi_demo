

<div class="row"> 
    <div class="span-6">
        <div class="par control-group form-control">
                <label class="control-label" for="customer_group_id">Customer Group <sup class="astric">*</sup></label>
            <div class="controls">
                    <span class="formwrapper"> 
                    <select name="customer_group_id" id="customer_group_id" class="chzn-select" onchange="getSalesRate(this)">
                            <option value="">Select Customer Group</option>

                                @forelse (getCustomerGroup() as $customer_group)
                        
                                <option value="{{ $customer_group->id }}">{{ $customer_group->customer_group_name}}</option>

                                @empty

                            @endforelse

                        </select>
                    </span>
            </div>
        </div>
    </div>
</div>


    <div class="widgetbox-inverse">

        <div class="headtitle">

            <h4 class="widgettitle"> Items List</h4>
            {{-- <h4 class="widgettitle">Raw Material Mapping</h4> --}}

        </div>

        <div class="widgetcontent overflow-scroll">



            <table class="table table-bordered responsive" id="pricelisttable">

                <thead>

                    <tr>

                        {{-- <th width="5%"><input type="checkbox" name="checkall" class="simple-check" id="checkall"/></th>
                         --}}
                        <th width="5%">Action</th>
                        {{-- <th >Sr No.</th> --}}
                        <th>Item</th>
                        <th>Code</th>
                        <th>Group</th>
                        <th width="13%">Sales Rate/Unit</th>
                        <th>Unit</th>
                        {{-- <th>Item Group</th> --}}

                    </tr>

                </thead>

                <tbody>
                   
                
                        @forelse(getPriceListDetails() as $key => $material)
                                
                                <tr>

                                        <input type="hidden" name="mid[]"  value={{ $key }}>
                                      
                                        <td><a onclick="removePriceListDetails(this)"><i class="action-icon iconfa-trash so_details"></i></a></td>

                                        {{-- <td class="sr_no"></td> --}}
                                  
                                        <td> <input type="hidden" name="item_id[]" id="item_id_{{$key}}"  class="itemClass" value="{{ $material->id }}"> {{ $material->item_name }} </td>

                                        <td> <input type="hidden" name="code[]" id="code" value="{{ $material->id }}"> {{ $material->item_code }} </td>

                                        <td> <input type="hidden" name="code[]" id="code" value="{{ $material->id }}"> {{ $material->item_group_name }} </td>
                                        
                                        <td><input type="text" name="sales_rate[]" id="sales_rate_{{ $material->id }}"  class="input-large auto-suggest isNumberKey" autocomplete="nope" onblur="formatPoints(this,2)" /> </td>
                                   
                                        <td> <input type="hidden" name="unit[]" id="unit" value="{{ $material->id }}"> {{ $material->unit_name }} </td>

                                        {{-- <td> <input type="hidden" name="group[]" id="group" value="{{ $material->id }}"> {{ $material->item_group_name }} </td> --}}

                                        
                                        
                                    </tr>

                                @empty
                                
                        
                        @endforelse
                                
                </tbody>

            </table><br>
            {{-- <button class="btn btn-primary" type="button" onclick="addPartDetail()">Add</button> --}}

           

        </div>

    </div>

    <script>
        
        function srNo() {
            jQuery('.sr_no').map(function (i, e) {
                jQuery(this).text(i + 1);
            });
        }

        srNo();

        var getItem = [<?php echo json_encode(getFittingAnyItem()); ?>];

        table = jQuery('#pricelisttable').DataTable({
            responsive: true,
            // "scrollX":true,
            pageLength : 50,  
            "order": false,  
            "oLanguage": {
            "sSearch": "Search :"
            },
        
        });

      
        </script>
    <script type="text/javascript" src="{{ asset('views/js/price_list.js?ver='.getJsVersion()) }}"></script>

