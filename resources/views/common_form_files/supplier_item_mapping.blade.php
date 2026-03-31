<?php
    use App\Models\SupplierItemMapping;
    if(isset($id)){    

        $supplierIds = SupplierItemMapping:: 
        leftJoin('suppliers', 'suppliers.id', '=', 'supplier_item_mapping.supplier_id')
        ->where('supplier_item_mapping.supplier_id', base64_decode($id))
        ->where('suppliers.status','!=','active')
        ->pluck('supplier_item_mapping.supplier_id')
        ->toArray();
                   
    }else {  

        $supplierIds = [];
    }
?>


<div class="row"> 
    <div class="span-6">
        <div class="par control-group form-control">
                <label class="control-label" for="supplier_id">Supplier <sup class="astric">*</sup></label>
            <div class="controls">
                    <span class="formwrapper"> 
                        <select name="supplier_id" id="supplier_id" class="chzn-select">
                            <option value="">Select Supplier</option>
                                @forelse (getSuppliers($supplierIds) as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->supplier_name}}</option>
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
            <h4 class="widgettitle">Items List </h4>
        </div>
        <div class="widgetcontent overflow-scroll">
         <?php
                    if(isset($id)){                     
                        $editItemIds = $getData->pluck('item_id')->toArray();
                        $editItems = $items->filter(function ($material) use ($editItemIds) {
                            return in_array($material->id, $editItemIds);
                        });
                        $otherItems = $items->filter(function ($material) use ($editItemIds) {
                            return !in_array($material->id, $editItemIds);
                        });
                    
                        $sortedRawMaterials = $editItems->merge($otherItems);
                    }else{
                        $sortedRawMaterials = getItemsForSupplierMapping();
                    }
            ?>
            <table class="table table-bordered responsive" id="supplierItemMappingTable">
                <thead>
                    <tr>
                        <th><input type="checkbox" name="checkall" class="simple-check" id="checkall"/></th>
                        <th>Item</th>
                        <th>Code</th>
                        <th>Group</th>
                        <th>Unit</th>
                    </tr>
                </thead>

                <tbody>
                        @forelse($sortedRawMaterials as $key => $items)
                                <tr>
                                        <input type="hidden" name="cid[]"  value={{ $key }}>

                                        <td>
                                            <input type="checkbox" class="simple-check" name="item_id[]" id="item_id_{{$items->id}}"  value={{ $items->id }}>
                                        </td>

                                        <td>
                                            <input type="hidden" name="item_name[]" id="item_name_{{$items->item_name}}"  class="itemClass" value="{{ $items->item_name }}"> {{ $items->item_name }} 
                                         </td>

                                        <td>
                                             <input type="hidden" name="code[]" id="code" value="{{ $items->id }}"> {{ $items->item_code }} 
                                        </td>

                                        <td> 
                                            <input type="hidden" name="code[]" id="code" value="{{ $items->id }}"> {{ $items->item_group_name }}
                                         </td>
                                   
                                        <td> 
                                            <input type="hidden" name="unit[]" id="unit" value="{{ $items->id }}"> {{ $items->unit_name }} 
                                        </td>
                                       
                                    </tr>
                                @empty
                        @endforelse
                </tbody>
            </table><br>
        </div>
    </div>
<script type="text/javascript" src="{{ asset('js/view/supplier_item_mapping.js?ver='.getJsVersion()) }}"></script>


