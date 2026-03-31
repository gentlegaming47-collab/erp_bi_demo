<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meat name="robots" content="noindex,nofollow">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="shortcut icon" type="image/x-icon" href="">

<!-- <meta name="csrf-token" contant="{{ csrf_token() }}"> -->
{{-- <title>@isset($pageTitle) {{ $pageTitle }} @endisset @if(!isset($isFront)) - Bhumi Irrigation Systems @endif</title> --}}
<title>@isset($pageTitle) {{ $pageTitle }} @endisset @if(!isset($isFront)) - Bhumi Polymers Pvt. Ltd. @endif</title>
<link rel="stylesheet" href="{{ asset('css/style.default.css?ver='.getJsVersion()) }}" type="text/css" />
<link rel="stylesheet" href="{{ asset('css/bootstrap-fileupload.min.css') }}" type="text/css" />
<link rel="stylesheet" href="{{ asset('css/bootstrap-timepicker.min.css') }}" type="text/css" />
<link rel="stylesheet" href="{{ asset('css/plugins/toastr/toastr.min.css') }}" type="text/css" />
<link rel="stylesheet" href="{{ asset('prettify/prettify.css') }}" type="text/css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/css/bootstrap-datetimepicker.min.css" type="text/css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/simple-line-icons/2.4.1/css/simple-line-icons.css" type="text/css" />

<link rel="stylesheet" href="{{ asset('css/responsive-tables.css') }}">

<link rel="stylesheet" href="{{ asset('css/style.navyblue.css') }}">

<link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/3.2.4/css/fixedColumns.bootstrap.min.css">

<script type="text/javascript" src="{{ asset('js/jquery-1.9.1.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/jquery-migrate-1.1.1.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/jquery-ui-1.10.3.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('prettify/prettify.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/dataTables.fixedHeader.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/plugins/toastr/toastr.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/jquery.alerts.js?ver='.getJsVersion()) }}"></script>
<script type="text/javascript" src="{{ asset('js/modernizr.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/bootstrap.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/colorpicker.js') }}"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="{{ asset('js/bootstrap-fileupload.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/bootstrap-timepicker.min.js') }}"></script>

<script type="text/javascript" src="{{ asset('js/jquery.cookie.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/jquery.uniform.min.js') }}"></script>
<!--<script type="text/javascript" src="{{ asset('js/flot/jquery.flot.min.js') }}"></script>-->
<!--<script type="text/javascript" src="{{ asset('js/flot/jquery.flot.resize.min.js') }}"></script>-->
<!--<script type="text/javascript" src="{{ asset('js/flot/jquery.flot.navigate.min.js') }}"></script>-->
<script type="text/javascript" src="{{ asset('js/chart/Chart.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/chart/hammer.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/chart/chart-plugin-zoom.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/responsive-tables.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/chosen.jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/jquery.slimscroll.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/jquery.validate.min.js') }}"></script>
{{-- <script type="text/javascript" src="{{ asset('views/js/modals/village.js') }}"></script> --}}
<script type="text/javascript" src="{{ asset('js/custom.js?ver='.getJsVersion()) }}"></script>
<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="js/excanvas.min.js"></script><![endif]-->

<script type="text/javascript" src="https://cdn.datatables.net/fixedcolumns/3.2.1/js/dataTables.fixedColumns.min.js"></script>
{{-- <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script> --}}
<script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script> --}}
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>
{{-- <script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js"></script> --}}
</head>

<body>

<div id="mainwrapper" class="mainwrapper">

    <div class="header">
        <div class="logo">
             {{-- <a href="{{ url('/') }}">BHUMI IRRIGATION</a> --}}
             <a href="{{ url('/') }}"><img src="{{asset('images/logo/bhumi_logo.jpg')}}" class="headingImages" alt="Atlanta"></a>         
        </div>
        <div class="headerinner">
            <ul class="headmenu">
                    @include('components.navigation')
                <li class="right">
                    <div class="userloggedinfo">
                   <a href=" {{ route('switch-company_year')}} "><h3 class="company-year">FY {{ defaultCompanyYear() }}</h3> </a>               

                   @php
                   if(Auth::id() != 1)
                   {
                       $getLocations =  App\Models\Location::join('user_locations', 'user_locations.company_unit_id', 'locations.id')->where('user_locations.user_id', Auth::id())->select('user_locations.company_unit_id', 'locations.id', 'locations.location_name')->distinct('location_name')->get();
                   }else{
                       $getLocations = App\Models\Location::all();
                   }
                   @endphp

                    {{-- @if(Auth::user()->user_type == "operator") --}}
                        @if(Auth::id() != 1)
                            @if($getLocations->count() > 1)                        
                                <a href=" {{ route('selectLocation')}} "><h3 class="company-year"> Switch Location </h3></a>  
                            @endif
                        @else 
                            <a href=" {{ route('selectLocation')}} " ><h3 class="company-year">Switch Location </h3></a> 
                        @endif
                    {{-- @endif --}}
                     
                    {{-- <h3 class="username">{{ Auth::user()->user_name }} </h3>                         --}}
                    {{-- <h3 class="locationName"> <span> User : </span>{{ Auth::user()->user_name }} </h3>       --}}
                    <h3 class="locationName name_as_is"> <span> User : </span>{{ Auth::user()->user_name }} </h3> 
                    
                    {{-- @if(Auth::user()->user_type == "operator") --}}
                    <h3 class="locationName"> <span> Location : </span> @if(getUserLocation() != ''){{ getUserLocation()}} @endif </h3>    
                    {{-- @endif                     --}}
                    
            
                        <div class="userinfo">

                             <ul>
                                <li><a href="{{ route('logout') }}" onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();"><i class="iconfa-signout action-icon"></i></a></li>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </ul>
                        </div>
                    </div>
                </li>
            </ul><!--headmenu-->
        </div>
    </div>

    {{-- <div class="leftpanel"> --}}
        <div class="leftmenu">

            

        </div><!--leftmenu-->

    {{-- </div><!-- leftpanel --> --}}

    <div class="rightpanel">

    @hasSection('header')
        @yield('header') <!-- Include bredcrumbs and tile -->
    @endif

        <div class="maincontent">
            <div class="maincontentinner">
                    @yield('content')

                @include('components.footer')<!--footer-->

            </div><!--maincontentinner-->
        </div><!--maincontent-->

    </div><!--rightpanel-->

</div><!--mainwrapper-->
<input type="hidden" id="rt_base_path" value="{{ route('/') }}"/>
<input type="hidden" id="upload_url" value="{{ asset('storage').'/' }}"/>
<input type="hidden" id="def_year_startdate" value="{{ getCurrentYearDates()['startdate'] }}" />
<input type="hidden" id="def_year_enddate" value="{{ getCurrentYearDates()['enddate'] }}" />

<!-- Modals Script -->
{{-- 
<script type="text/javascript" src="{{ asset('views/js/modals/country_modal.js') }}"></script>
<script type="text/javascript" src="{{ asset('views/js/modals/state_modal.js') }}"></script>

<script type="text/javascript" src="{{ asset('views/js/modals/city_modal.js') }}"></script>
<script type="text/javascript" src="{{ asset('views/js/modals/material_modal.js') }}"></script>
<script type="text/javascript" src="{{ asset('views/js/modals/customer_modal.js') }}"></script>
<script type="text/javascript" src="{{ asset('views/js/modals/item_list_modal.js') }}"></script>
<script type="text/javascript" src="{{ asset('views/js/modals/unit_modal.js') }}"></script>
<script type="text/javascript" src="{{ asset('views/js/modals/customer_group.js') }}"></script>
<script type="text/javascript" src="{{ asset('views/js/modals/raw_material_group.js') }}"></script>
<script type="text/javascript" src="{{ asset('views/js/modals/item_group_modal.js') }}"></script>  --}}
<script type="text/javascript" src="{{ asset('views/js/common.js?ver='.getJsVersion()) }}"></script>


<!-- Modals Script END-->
@hasSection('scripts')
    @yield('scripts') <!-- Render script -->
@endif
<script type="text/javascript">
    jQuery(document).ready(function() {

        //tooltip
        jQuery('i[rel=tooltip]').tooltip();

        //datepicker
        jQuery('#datepicker').datepicker();

        // tabbed widget
        jQuery('.tabbedwidget').tabs();

    });

var from_date = jQuery("#def_year_startdate").val();
var end_date = jQuery("#def_year_enddate").val();

from_date = from_date.split('-').reverse().join('/');
end_date = end_date.split('-').reverse().join('/');


jQuery("#psfdSearchForm #trans_from_date").val(from_date);
jQuery("#psfdSearchForm #trans_to_date").val(end_date);

</script>
</body>
</html>
