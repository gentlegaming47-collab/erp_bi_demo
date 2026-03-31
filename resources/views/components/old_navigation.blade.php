<ul class="nav nav-tabs nav-stacked">
    <li class="nav-header">Navigation</li>
    {{-- <li class="@if( isActivePage('dashboard',array(),true) )active @endif">
        <a href="{{ route('dashboard') }}"><span class="iconfa-laptop"></span> Dashboard</a>
    </li> --}}
    @if(hasAccess(['material','city',  'taluka', 'village','country','customer-group','transporter', 'location', 'item-group', 'raw-material-group', 'raw-material',  'item', 'supplier','state','hsn_code','customer','unit','customer','item-raw-material-mapping','raw-material'],'manage'))
    <li class="org-text dropdown @if( isActivePages(['material','city','country',  'taluka', 'village', 'customer-group', 'transporter', 'location', 'item-group', 'raw-material-group', 'raw-material', 'hsn_code','supplier','state','customer','unit','customer','item','item-raw-material-mapping','raw-material']) || isActivePage('switch-company_year',array(),true))active @endif">
        <a href=""><span class="iconfa-pencil"></span> Master</a>
        <ul style="@if( isActivePages(['material','city','country', 'village', 'customer-group', 'state','hsn_code', 'transporter',  'item-group', 'raw-material-group','supplier','customer','unit','customer','item','taluka','location','item-raw-material-mapping','raw-material']) || isActivePage('switch-company_year',array(),true)) display: block; @endif">
            @if(hasAccess("country","manage"))
            <li class="@if( isActivePage('country') ) active @endif"><a href="{{ route('manage-country') }}">Country</a></li>
            @endif
            @if(hasAccess("state","manage"))
            <li class="@if( isActivePage('state') ) active @endif"><a href="{{ route('manage-state') }}">State</a></li>
            @endif
            @if(hasAccess("city","manage"))
            <li class="@if( isActivePage('city') ) active @endif"><a href="{{ route('manage-city') }}">District</a></li>
            @endif
            @if(hasAccess("taluka","manage"))
            <li class="@if( isActivePage('taluka') ) active @endif"><a href="{{ route('manage-taluka') }}">Taluka</a></li>
            @endif            
            @if(hasAccess("village","manage"))
            <li class="@if( isActivePage('village') ) active @endif"><a href="{{ route('manage-village') }}">Village</a></li>
            @endif
             @if(hasAccess("location","manage"))
            <li class="@if( isActivePage('location') ) active @endif"><a href="{{ route('manage-location') }}">Location</a></li>
            @endif
            @if(hasAccess("customer-group","manage"))
            <li class="@if( isActivePage('customer-group') ) active @endif"><a href="{{ route('manage-customer-group') }}">Customer Group</a></li>
            @endif
            @if(hasAccess("customer","manage"))
            <li class="@if( isActivePage('customer') ) active @endif"><a href="{{ route('manage-customer') }}">Customer</a></li>
            @endif
            @if(hasAccess("supplier","manage"))
            <li class="@if( isActivePage('supplier') ) active @endif"><a href="{{ route('manage-supplier') }}">Supplier</a></li>
            @endif
            @if(hasAccess("transporter","manage"))
            <li class="@if( isActivePage('transporter') ) active @endif"><a href="{{ route('manage-transporter') }}">Transporter</a></li>
            @endif
            @if(hasAccess("hsn_code","manage"))
            <li class="@if( isActivePage('hsn_code') ) active @endif"><a href="{{ route('manage-hsn_code') }}"> HSN Code</a></li>
            @endif
            @if(hasAccess("unit","manage"))
            <li class="@if( isActivePage('unit') ) active-link @endif"><a href="{{ route('manage-unit') }}"> Unit</a></li>    
            @endif
            @if(hasAccess("item-group","manage"))
            <li class="@if( isActivePage('item-group') ) active @endif"><a href="{{ route('manage-item-group') }}">Item Group</a></li>
            @endif
            @if(hasAccess("raw-material-group","manage"))
            <li class="@if( isActivePage('raw-material-group') ) active @endif"><a href="{{ route('manage-raw-material-group') }}">Raw Material Group</a></li>
            @endif
            @if(hasAccess("item","manage"))
            <li class="@if( isActivePage('item') ) active @endif"><a href="{{ route('manage-item') }}">Item</a></li>
            @endif
            
            @if(hasAccess("raw-material","manage"))
            <li class="@if( isActivePage('raw-material') ) active @endif"><a href="{{ route('manage-raw-material') }}">Raw Material </a></li>
            @endif  
            
            @if(hasAccess("item-raw-material_mapping","manage"))
            <li class="@if( isActivePage('item-raw-material_mapping') ) active @endif"><a href="{{ route('manage-item-raw-matrial-mapping') }}">Item Raw Material Mapping</a></li>
            @endif         
            
                 
          
         
            <li class="@if( isActivePage('switch-company_year',array(),true) ) active @endif"><a href="{{ route('switch-company_year') }}">Switch Year</a></li>
         
        </ul>
    </li>
    @endif
   
    @if(hasAccess(['user','company_year'],'manage') || hasAccess("user_access","edit"))
    <li class="dropdown @if( isActivePages(['user','company_year']) || isActivePage('edit-user_access',array(),true))active @endif">
        <a href=""><span class="iconfa-user"></span> Admin</a>
        <ul style="@if( isActivePages(['user','company_year']) || isActivePage('edit-user_access',array(),true)) display: block; @endif">
            @if(hasAccess("user","manage"))
            <li class="@if( isActivePage('user') ) active @endif"><a href="{{ route('manage-user') }}"> User</a></li>
            @endif
            @if(hasAccess("user_access","manage"))
            <li class="@if( isActivePage('edit-user_access',array(),true) ) active @endif"><a href="{{ route('edit-user_access') }}"> User Access</a></li>
            @endif
            @if(hasAccess("company_year","manage"))
            <li class="@if( isActivePage('company_year') ) active @endif"><a href="{{ route('manage-company_year') }}"> Company Year</a></li>
            @endif
        </ul>
    </li>
    @endif
</ul>