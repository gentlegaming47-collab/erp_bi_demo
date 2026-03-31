
           @forelse (accessModule() as $module)
       
                @if(hasAccess((accessMenu($module->id)),'manage'))
           
                                @if($module->display_name == "Master" && isActivePage('switch-company_year',array(),true))
                                    <li class=" @if( isActivePages(accessMenu($module->id)) || isActivePage('switch-company_year',array(),true)) active  @endif">            
                                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                            <span class="headmenu-label"> {{ strtolower($module->display_name) }}
                                            </span>                                        
                                        </a>
                                @else
                                    <li class=" @if( isActivePages(accessMenu($module->id)) ) active  @endif">            
                                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                            <span class="headmenu-label"> {{ strtolower($module->display_name) }}
                                            </span>                                        
                                        </a>
                                @endif
                            
                                 <ul  class="sub-menu">

                            @foreach ( manageAccessMenu($module->id) as $menus)

                                    <?php 
                                        $passRoute = $menus->page == "user_access" ? "edit-" : "manage-";
                                    ?>
                                        @if($menus->page != 'switch_year')  
                                                
                                            @if(hasAccess($menus->page,"manage"))
                                                {{-- @if(Auth::user()->id != 1 ) --}}
                                         
                                                {{-- @if(strtolower($menus->page) == 'sm_approval' || strtolower($menus->page) == 'md_approval' || strtolower($menus->page) == 'zsm_approval')

                                                    @if(Auth::user()->user_type == "zonal_manager")
                                                        <li class="@if( isActivePage('zsm_approval',array(),true) ) active-link @endif"><a href="{{ route('manage-zsm_approval') }}">Zonal Approval</a></li>
                                                    @endif
                    
                                                    @if(Auth::user()->user_type == "director")
                                                        <li class="@if( isActivePage('md_approval',array(),true) ) active-link @endif"><a href="{{ route('manage-md_approval') }}">MD Approval</a></li>
                                                    @endif
                    
                                                    @if(Auth::user()->user_type == "state_manager")
                                                        <li class="@if( isActivePage('sm_approval',array(),true) ) active-link @endif"><a href="{{ route('manage-sm_approval') }}">SM Approval</a></li>
                                                    @endif                     
                                         
                                                @else                                                 --}}
                                                
                                                {{-- <li class="@if( isActivePage( $menus->page)) active-link @endif"><a href="{{ route($passRoute. $menus->page) }}" >{{ $menus->display_name }}</a></li> 
                                                @endif

                                            @else --}}
                                           
                                            <li class="@if( isActivePage( $menus->page)) active-link @endif"><a href="{{ route($passRoute. $menus->page) }}" >{{ $menus->display_name }}</a></li> 
                                            {{-- @endif --}}

                                            @endif 
                                        @else
                                         
                                        @endif 
                                        
                            @endforeach
                            {{-- @dd($module->display_name) --}}
                            @if($module->display_name == "Master")          
                  
                            <li class="@if( isActivePage('switch-company_year',array(),true) ) active-link @endif"><a href="{{ route('switch-company_year') }}">Switch Year</a></li>
                            @endif


                        </ul>
                @endif                   
        @empty  
    @endforelse
            
        
